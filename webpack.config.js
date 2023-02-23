// used plugins / utilities

const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const MergeIntoSingleFile = require('webpack-merge-and-include-globally');
const WatchExternalFilesPlugin = require('webpack-watch-files-plugin');
const postcss = require('postcss');
const combineSelectors = require('postcss-combine-duplicated-selectors');
const helpers = require('./webpack.helper');

// input / output paths

const BUILD_DIR = path.resolve(__dirname, './www/webpack');
const APP_DIR = path.resolve(__dirname, './');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production'; // get webpack current mode
    const isWatching = argv.watch;
    return {
        entry: {
            stylesWeb: [
                APP_DIR + '/www/styles/appwork.css',
                APP_DIR + '/www/styles/imports.scss',
                APP_DIR + '/www/styles/web/web.scss'
            ],
            scriptsWeb: [
                APP_DIR + '/www/js/web/web.js',
            ],
            stylesAdmin: [
                APP_DIR + '/node_modules/easymde/dist/easymde.min.css',
                APP_DIR + '/www/styles/admin/EasyMDE.css',
                APP_DIR + '/www/styles/appwork.css',
                APP_DIR + '/www/styles/imports.scss',
                APP_DIR + '/www/styles/admin/admin.css'
            ],
        },
        output: {
            path: BUILD_DIR,
            filename: "[name].bundle.js"
        },
        module: {
            rules: [
                // handle js
                {
                    test: /\.(js)$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env']
                        }
                    }
                },
                // handle css
                {
                    test: /\.css$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader, // create bundle -> extract to separated file
                        },
                        {
                            loader: 'css-loader',
                            options: {
                                url: false,
                                sourceMap: true
                            }
                        },
                        {
                            loader: 'import-glob-loader'
                        }
                    ]
                },
                // handle sass
                {
                    test: /\.s[ac]ss$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader, // create bundle -> extract to separated file
                        },
                        {
                            loader: 'css-loader',
                            options: {
                                url: false,
                                sourceMap: true
                            }
                        },
                        {
                            loader: 'sass-loader'
                        },
                        {
                            loader: 'import-glob-loader'
                        }
                    ]
                },
                {
                    test: /\.(woff|woff2|eot|ttf|otf)$/,
                    loader: "file-loader",
                    options: {
                        outputPath: APP_DIR + "/www/fonts",
                    }
                }
            ]
        },
        resolve: {
            extensions: ['.js']
        },
        externals: {
            jquery: 'jQuery',
            $: 'jQuery'
        },
        plugins: [
            new FixStyleOnlyEntriesPlugin(),
            new MiniCssExtractPlugin({
                filename: "[name].bundle.css",
            }),
            new MergeIntoSingleFile({
                files: helpers.generateArrayForMerge(APP_DIR, isProduction)
            }, function () {}),
            isWatching ? new WatchExternalFilesPlugin.default({
                files: [
                    APP_DIR + '/www/js/**/*.js'
                ],
                verbose: true
            }) : null
        ].filter(Boolean),
        optimization: {
            minimizer: [
                // new OptimizeCSSAssetsPlugin(
                //     {
                //         cssProcessor: postcss([combineSelectors({removeDuplicatedProperties: true})]),
                //     }
                // ),
                new UglifyJsPlugin({
                    test: /\.js(\?.*)?$/i,
                }),
            ],
        },
        watchOptions: {
            aggregateTimeout: 300,
            poll: 1000
        },
    }
}