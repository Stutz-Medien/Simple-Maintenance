const esbuild = require('esbuild');
const sassPlugin = require('esbuild-plugin-sass');
const fs = require('fs-extra');
const glob = require('glob');
const chokidar = require('chokidar');
const path = require('path');

// Function to generate mix-manifest.json
const generateMixManifest = (outputDir) => {
    const manifest = {};
    const jsAndCssFiles = glob.sync(`${outputDir}/{js,css}/**/*.{js,css}`, {
        nodir: true,
    });
    jsAndCssFiles.forEach((file) => {
        const relativePath = file.replace(`${outputDir}/`, '');
        manifest[`/${relativePath}`] = `/${relativePath}`;
    });

    fs.writeFileSync(
        `${outputDir}/mix-manifest.json`,
        JSON.stringify(manifest, null, 2)
    );
};

// Main build function
const build = async () => {
    try {
        await Promise.all([
            esbuild.build({
                entryPoints: ['assets/src/scripts/app.ts'],
                bundle: true,
                outdir: 'assets/dist/js',
                minify: process.env.NODE_ENV === 'production',
                sourcemap: process.env.NODE_ENV !== 'production',
                loader: {
                    '.jsx': 'jsx',
                },
            }),
            esbuild.build({
                entryPoints: ['assets/src/sass/style.scss'],
                outdir: 'assets/dist/css',
                plugins: [sassPlugin()],
                minify: process.env.NODE_ENV === 'production',
            }),
        ]);

        // Post-build steps
        generateMixManifest('assets/dist');
    } catch (error) {
        console.error('Build failed:', error);

        if (process.env.WATCH !== 'true') {
            process.exit(1);
        }
    }
};

// Watch function
const startWatch = () => {
    const watcher = chokidar.watch('assets/src', {
        ignored: /(^|[\/\\])\../,
        persistent: true,
    });

    watcher.on('change', (path) => {
        console.log(`File ${path} has been changed. Rebuilding...`);
        build();
    });

    console.log('Watching for changes...');
    build(); // Initial build
};

if (process.env.WATCH === 'true') {
    startWatch();
} else {
    build();
}
