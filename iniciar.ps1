param(
    [switch]$NoServe,
    [switch]$NoBrowser
)

$ErrorActionPreference = "Stop"
$root = $PSScriptRoot
Set-Location $root

function Write-Step($message) {
    Write-Host ""
    Write-Host "==> $message" -ForegroundColor Cyan
}

function Fail($message) {
    Write-Host ""
    Write-Host "ERROR: $message" -ForegroundColor Red
    exit 1
}

Write-Host "Multiservicios Las Acacias - inicio local" -ForegroundColor Green
Write-Host "Carpeta: $root"

# PHP: usar el del PATH o el de XAMPP
$php = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $php) {
    $xamppPhp = "C:\xampp\php\php.exe"
    if (Test-Path $xamppPhp) {
        $php = $xamppPhp
        $env:Path = "C:\xampp\php;$env:Path"
    } else {
        Fail "PHP no encontrado. Instala PHP 8.2+ o XAMPP y agrega C:\xampp\php al PATH."
    }
}

# Composer
$composer = (Get-Command composer -ErrorAction SilentlyContinue).Source
if (-not $composer) {
    Fail "Composer no encontrado. Instala Composer y agregalo al PATH."
}

Write-Host "PHP: $(& $php -r 'echo PHP_VERSION;')"
Write-Host "Composer: $composer"

# Dependencias PHP
if (-not (Test-Path "vendor\autoload.php")) {
    Write-Step "Instalando dependencias de PHP (puede tardar la primera vez)..."
    & $composer install --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) { Fail "Composer install fallo." }
} else {
    Write-Step "Dependencias de PHP ya instaladas."
}

# Archivo .env
if (-not (Test-Path ".env")) {
    Write-Step "Creando archivo .env desde .env.example..."
    Copy-Item ".env.example" ".env"
}

$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "(?m)^APP_KEY=base64:") {
    Write-Step "Generando APP_KEY..."
    & $php artisan key:generate --force
    if ($LASTEXITCODE -ne 0) { Fail "No se pudo generar APP_KEY." }
}

# Base SQLite (solo si .env usa SQLite)
if ($envContent -match "(?m)^DB_CONNECTION=sqlite") {
    if (-not (Test-Path "database\database.sqlite")) {
        Write-Step "Creando base de datos SQLite..."
        New-Item -ItemType File -Path "database\database.sqlite" | Out-Null
    }
}

# Migraciones
Write-Step "Aplicando migraciones..."
& $php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Fail "Las migraciones fallaron. Si usas MySQL, inicia MySQL en XAMPP y revisa .env."
}

if ($NoServe) {
    Write-Host ""
    Write-Host "Configuracion completada (modo -NoServe)." -ForegroundColor Green
    exit 0
}

# Buscar un puerto libre desde 8000
function Test-Port([int]$Port) {
    $client = New-Object System.Net.Sockets.TcpClient
    try {
        $client.Connect("127.0.0.1", $Port)
        $client.Close()
        return $true
    } catch {
        return $false
    } finally {
        if ($client) { $client.Dispose() }
    }
}

$port = 8000
while (Test-Port $port) { $port++ }
$url = "http://127.0.0.1:$port"

Write-Step "Iniciando servidor en $url..."
Start-Process -FilePath $php -ArgumentList @("artisan", "serve", "--host=127.0.0.1", "--port=$port") -WorkingDirectory $root

# Esperar a que el servidor responda
$ready = $false
for ($i = 0; $i -lt 30; $i++) {
    try {
        $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 2
        if ($response.StatusCode -ge 200 -and $response.StatusCode -lt 500) {
            $ready = $true
            break
        }
    } catch {
    }
    Start-Sleep -Milliseconds 500
}

if (-not $ready) {
    Fail "El servidor no respondio. Revisa la ventana del servidor para ver el error."
}

Write-Host ""
Write-Host "Servidor listo: $url" -ForegroundColor Green
Write-Host "Usuario: admin@acacias.com"
Write-Host "Clave:   admin123"
Write-Host ""
Write-Host "No cierres la ventana del servidor. Para detenerlo, cierra esa ventana o pulsa Ctrl+C en ella." -ForegroundColor Yellow

if (-not $NoBrowser) {
    Start-Process $url
}
