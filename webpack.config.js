const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    entry: {
        main: './GymMel_Alumni/assets/js/index.js',
        styles: './GymMel_Alumni/assets/scss/style.scss'
    },
    output: {
        path: path.resolve(__dirname, 'GymMel_Alumni/assets/dist')
    },
    mode: 'production',
    watchOptions: {
        aggregateTimeout: 200,
        poll: 1000
    },
    devtool: false,
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'styles.css'
        })
    ]
};


