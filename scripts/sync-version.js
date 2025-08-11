#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

// Read version from package.json
const packageJson = JSON.parse(fs.readFileSync('package.json', 'utf8'));
const version = packageJson.version;

console.log(`Syncing version to ${version}...`);

// Update main plugin file
const pluginFile = 'captcha-for-elementor-pro-forms.php';
let pluginContent = fs.readFileSync(pluginFile, 'utf8');

// Update Version header
pluginContent = pluginContent.replace(
    /(\* Version:\s+)[\d.]+/,
    `$1${version}`
);

// Update CEPF_VERSION constant
pluginContent = pluginContent.replace(
    /(define\('CEPF_VERSION',\s+')[\d.]+('\);)/,
    `$1${version}$2`
);

fs.writeFileSync(pluginFile, pluginContent);

// Update readme.txt
const readmeFile = 'readme.txt';
let readmeContent = fs.readFileSync(readmeFile, 'utf8');

// Update Stable tag
readmeContent = readmeContent.replace(
    /(Stable tag:\s+)[\d.]+/,
    `$1${version}`
);

fs.writeFileSync(readmeFile, readmeContent);

console.log('Version sync complete!');
console.log(`- Plugin file: ${version}`);
console.log(`- README: ${version}`);
console.log(`- Package.json: ${version}`);