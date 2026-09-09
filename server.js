import { spawn, execSync } from 'child_process';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Verify PHP is available, or install it non-interactively
try {
  execSync('which php', { stdio: 'ignore' });
} catch {
  console.log('PHP runtime not found. Provisioning php-cli and php-sqlite3...');
  try {
    execSync('DEBIAN_FRONTEND=noninteractive apt-get update -y && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" php-cli php-sqlite3', { stdio: 'inherit' });
  } catch (err) {
    console.error('Error provisioning PHP:', err);
  }
}

console.log("Starting Caroline's Place PHP Web Server on port 3000 with router.php...");
const php = spawn('php', ['-S', '0.0.0.0:3000', 'router.php'], {
  cwd: __dirname,
  stdio: 'inherit'
});

php.on('error', (err) => {
  console.error('PHP server error:', err);
});

php.on('exit', (code, signal) => {
  console.log(`PHP server stopped (code: ${code}, signal: ${signal})`);
  process.exit(code ?? 0);
});

const shutdown = (signal) => {
  if (php && !php.killed) {
    php.kill(signal);
  }
};

process.on('SIGINT', () => shutdown('SIGINT'));
process.on('SIGTERM', () => shutdown('SIGTERM'));
