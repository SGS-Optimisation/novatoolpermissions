import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';
import {homedir} from 'os'
import {resolve} from 'path'
import { webUpdateNotice } from '@plugin-web-update-notification/vite'

let host='dagobah.test'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        webUpdateNotice({
            logVersion: true,
            notificationProps: {
                title: 'Application update',
                description: 'The application has been updated, please refresh the page',
                buttonText: 'refresh',
            },
        }),

    ],
    esbuild: {
        jsxFactory: 'h',
        jsxFragment: 'Fragment'
    },

    resolve: {
        alias: {
            '@': '/resources/js'
        }
    },
    server: detectServerConfig(host),

});

function detectServerConfig(host) {
    let keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`)
    let certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`)

    if (!fs.existsSync(keyPath)) {
        return {}
    }

    if (!fs.existsSync(certificatePath)) {
        return {}
    }

    return {
        hmr: {host},
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
    }
}
