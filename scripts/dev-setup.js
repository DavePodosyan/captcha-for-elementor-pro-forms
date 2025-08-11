#!/usr/bin/env node

import fs from 'fs';
import path from 'path';
import { exec } from 'child_process';

console.log('🚀 Setting up development environment...');

// Ensure required directories exist
const dirs = ['dist', 'releases', 'languages'];
dirs.forEach((dir) => {
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
        console.log(`✅ Created ${dir}/ directory`);
    }
});

// Make scripts executable (if on Unix-like systems)
if (process.platform !== 'win32') {
    try {
        exec('chmod +x scripts/*.js', (error) => {
            if (error) {
                console.log('ℹ️  Could not make scripts executable (this is OK on some systems)');
            } else {
                console.log('✅ Made scripts executable');
            }
        });
    } catch (error) {
        console.log('ℹ️  Could not make scripts executable (this is OK)');
    }
}

console.log('🎉 Development environment ready!');
console.log('');
console.log('Available commands:');
console.log('  npm run dev      - Lint and build');
console.log('  npm run build    - Create release ZIP');
console.log('  npm run lint     - Run all linting');
console.log('  npm run format   - Format all code');
console.log('  npm run watch    - Watch JS files for changes');
console.log('  npm run release  - Prepare release ZIP');
