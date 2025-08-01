/**
 * External dependencies
 */
import fs from 'fs';
import path from 'path';

/**
 * WordPress dependencies
 */
import defaultConfig from '@wordpress/scripts/config/webpack.config.js';

const isProduction = process.env.NODE_ENV === 'production';

// Extend the default config.
const sharedConfig = {
    ...defaultConfig,
    output: {
        path: path.resolve(process.cwd(), 'build', 'js'),
        filename: '[name].js',
        chunkFilename: '[name].js',
    },
    devtool: isProduction ? false : 'source-map',
    optimization: {
        ...defaultConfig.optimization,
        splitChunks: {
            ...defaultConfig.optimization.splitChunks,
        },
        minimizer: [...(defaultConfig.optimization?.minimizer || [])],
    },
};

const jsFiles = {
    ...sharedConfig,
    entry: {
        main: path.resolve(process.cwd(), 'src', 'js', 'main.js'),
        'media-frame': path.resolve(
            process.cwd(),
            'src',
            'js',
            'media-frame.js'
        ),
    },
};

export default [jsFiles];
