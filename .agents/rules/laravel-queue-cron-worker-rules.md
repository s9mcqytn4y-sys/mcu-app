---
trigger: glob
globs: **/Jobs/**/*, **/Console/Commands/**/*, **/Console/Kernel.php, **/routes/console.php, **/config/queue.php, **/config/horizon.php, **/*Job.php, **/*Command.php, **/*Worker*.php
---

# LARAVEL 13.X JOB QUEUE, CRON SCHEDULER & WORKER ARCHITECTURE RULES

## 1. QUEUE & WORKER ARCHITECTURE OVERVIEW

All asynchronous background processing in Laravel 13.x MUST operate through a structured, multi-queue topology powered by Redis/Valkey drivers and monitored via Laravel Horizon or Supervisor.

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   LARAVEL 13.X QUEUE WORKFLOW ENGINE                   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    ▼                               ▼                               ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ 1. HIGH PRIORITY      │ │ 2. DEFAULT PRIORITY   │ │ 3. LOW / BATCH / CRON │
│    (`high`)           │ │    (`default`)        │ │    (`low` / `batch`)   │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • OTP / Security SMS  │ │ • Standard Emails     │ │ • Export CSV / PDF    │
│ • Real-time Webhooks  │ │ • Image Processing    │ │ • Daily Cron Re-

index │
│ • Payment Push Sync   │ │ • Order Status Updates│ │ • Analytics Sync      │

Queue Driver Directives:Production Driver: MUST use redis or valkey. The database queue driver is permitted ONLY for low-traffic staging environments. The sync driver is STRICTLY FORBIDDEN in Production.Multi-Queue Priority Execution: Workers MUST process queues in order of priority:Bashphp artisan queue:work redis --queue=high,default,low,notifications
2. JOB DESIGN, DATA SERIALIZATION & IDEMPOTENCY RULESA. Model Serialization vs Primitive PassingAvoid Heavy Payload Bloat: Avoid passing large, complex object instances into job constructors. Pass Primary Identifiers (IDs/UUIDs) and re-query the model inside the handle() method.Graceful Stale Model Handling: If passing Eloquent Models (SerializesModels), enable soft-deletion handling or check if the model still exists inside handle().B. Transaction Safety Rule (afterCommit)CRITICAL RULE: When dispatching a job from inside a Database Transaction, ALWAYS use afterCommit() or enable queue.of_transactions config to prevent workers from attempting to process records before the DB transaction has committed.PHP// ✅ BENAR: Menggunakan afterCommit untuk mencegah Race Condition di Worker
DB::transaction(function () use ($order) {$order->update(['status' => 'PAID']);

    // Job hanya dikirim SETELAH transaksi database berhasil di-COMMIT
    ProcessOrderFulfillmentJob::dispatch($order->id)->afterCommit();
});
C. Strict Job Idempotency PatternEvery queued job MUST be idempotent—executing the same job twice with identical arguments MUST NOT produce duplicate side-effects (e.g., double payments or duplicate emails).PHPnamespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class ProcessPaymentWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $eventId,
        public readonly int $orderId
    ) {}

    public function handle(): void
    {
        // 1. Idempotency Lock Check via Redis
        $lockKey = "job:idempotency:{$this->eventId}";
        $isFirstExecution = Redis::set($lockKey, 'PROCESSING', 'EX', 86400, 'NX');

        if (!$isFirstExecution) {
            // Job sudah pernah diproses atau sedang berjalan
            return;
        }

        // 2. Eksekusi Logika Bisnis
        $order = Order::find($this->orderId);
        if (!$order \vert{}\vert{}$order->is_paid) {
            return;
        }

        $order->markAsPaid();
    }
}
3. RETRIES, TIMEOUTS, EXPONENTIAL BACKOFF & UNIQUE JOBSA. Retry & Timeout ConfigurationEvery Job MUST define explicit limits for execution time and retry policies:PHPnamespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateMonthlyReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah maksimal percobaan eksekusi.
     */
    public int $tries = 3;

    /**
     * Batas waktu maksimal eksekusi job (dalam detik).
     */
    public int $timeout = 120;

    /**
     * Menghitung jeda waktu antar percobaan (Exponential Backoff).
     * Percobaan 1: 5s, Percobaan 2: 25s, Percobaan 3: 125s
     */
    public function backoff(): array
    {
        return [5, 25, 125];
    }

    /**
     * Menentukan kapan job harus berhenti dicoba lagi jika error spesifik terjadi.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2);
    }
}
B. Unique Jobs (ShouldBeUnique)To prevent duplicate concurrent jobs (e.g., user clicking "Export PDF" 10 times simultaneously):PHPuse Illuminate\Contracts\Queue\ShouldBeUnique;

class ExportUsersCsvJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(public int $userId) {}

    /**
     * Kunci unik untuk memblokir duplikasi job yang sedang berjalan.
     */
    public function uniqueId(): string
    {
        return (string) $this->userId;
    }

    /**
     * Durasi kuncian unik (dalam detik).
     */
    public int $uniqueFor = 600;
}
C. Dead Letter Queue & Failed Job HandlingWhen a job exhausts all retry attempts, it MUST invoke the failed() callback to alert the system or post to monitoring tools:PHPpublic function failed(?Throwable $exception): void
{
    Log::critical("Job ProcessOrderFulfillmentJob GAGAL Permanen!", [
        'order_id' => $this->orderId,
        'error' => $exception?->getMessage()
    ]);

    // Kirim notifikasi ke tim Engineering via Slack / PagerDuty
    Notification::route('slack', config('logging.channels.slack.url'))
        ->notify(new JobFailedAlertNotification($this->orderId,$exception->getMessage()));
}
4. SCHEDULED TASKS (CRON JOBS IN LARAVEL 13.X)In Laravel 13.x, scheduled tasks are configured directly in routes/console.php using the Schedule facade.A. Overlapping & Multi-Server Execution RulesOverlapping Prevention: Long-running scheduled tasks MUST specify withoutOverlapping().Single Server Execution: In multi-server / horizontal scaling environments, tasks MUST specify onOneServer() to prevent running the same cron job simultaneously across 5 server instances.PHP// routes/console.php (Laravel 13.x Standard)
use Illuminate\Support\Facades\Schedule;

// 1. Task Harian dengan Overlapping Guard & Single Server Lock
Schedule::command('app:sync-inventory-stock')
    ->dailyAt('02:00')
    ->withoutOverlapping(60) // Lock maksimal 60 menit
    ->onOneServer()
    ->runInBackground()
    ->onFailure(function () {
        Log::error("Cron Job Stock Sync Gagal Dieksekusi!");
    });

// 2. Task Pembersihan Log Setiap Jam
Schedule::command('queue:prune-failed --hours=48')
    ->hourly()
    ->onOneServer();
5. LARAVEL HORIZON, SUPERVISOR & INFRASTRUCTURE TUNINGA. Supervisor Configuration (/etc/supervisor/conf.d/laravel-worker.conf)For non-Horizon environments, run daemonized queue workers using Supervisor:Ini, TOML[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-jobs=1000 --max-time=3600 --memory=128
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600
B. Laravel Horizon Configuration (config/horizon.php)When using Redis/Valkey, prefer Laravel Horizon for auto-scaling workers and visual telemetry:PHP'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['high', 'default'],
            'balance' => 'auto', // Auto-scaling jumlah worker sesuai beban antrean
            'autoScalingStrategy' => 'time',
            'minProcesses' => 3,
            'maxProcesses' => 20,
            'balanceMaxShift' => 2,
            'balanceCooldown' => 3,
            'tries' => 3,
            'timeout' => 90,
            'memory' => 128,
        ],
        'supervisor-low' => [
            'connection' => 'redis',
            'queue' => ['low', 'batch'],
            'balance' => 'simple',
            'processes' => 2,
            'tries' => 1,
            'timeout' => 300,
            'memory' => 256,
        ],
    ],
],
C. Zero-Downtime Deployment DirectiveDuring CI/CD deployments, workers MUST be restarted gracefully to load updated application code:Supervisor: Run php artisan queue:restart post-deployment.Horizon: Run php artisan horizon:terminate post-deployment.6. EDGE CASES & FAILURE MITIGATION MATRIXEdge Case ScenarioRoot CauseRequired System Mitigation RuleWorker Processing Deleted ModelDelay between dispatch and execution; record deleted in DB.Enable public bool $deleteWhenMissingModels = true; on the Job class.Worker Memory LeakLong-running worker process accumulates in-memory state.Set --max-jobs=1000 and --max-time=3600 so workers exit gracefully and refresh memory.Job Dispatched Before DB CommitFast worker picks up job before DB transaction completes.Always use ->afterCommit() when dispatching inside DB::transaction().High Queue StarvationUnbounded low-priority jobs block high-priority jobs.Configure explicit worker queue processing order (high,default,low) or separate Supervisor process pools.Deadlock on Simultaneous Cron JobsMultiple servers running the same scheduled task.Add ->onOneServer() and ->withoutOverlapping() to scheduled routes in routes/console.php.7. AGENT EXECUTION DIRECTIVESZero Sync Drivers in Production: Never generate production configurations using the sync queue driver.Enforce Transaction Safety: Always incorporate afterCommit() or set queue.of_transactions = true when dispatching jobs inside database transactions.Apply "Fix Terkecil yang Aman": Preserve job retry logic, backoff formulas, unique keys, and Horizon/Supervisor process limits when updating queue or command modules.
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘
