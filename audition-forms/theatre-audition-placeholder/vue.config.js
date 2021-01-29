module.exports = {
    productionSourceMap: false,
    publicPath: process.env.NODE_ENV === 'production'
        ? 'wordpress/wp-content/themes/cah-spa/audition-forms/theatre-audition-form/dist'
        : 'http://localhost:8080',
    devServer: {
        proxy: 'http://localhost/wordpress/wp-content/themes/cah-spa/',
    },
    outputDir: './dist',
    configureWebpack: {
        devServer: {
            contentBase: '/wp-content/themes/cah-spa/audition-forms/theatre-audition-placeholder/dist/',
            allowedHosts: ['localhost/wordpress'],
            headers: {
                'Access-Control-Allow-Origin': '*',
            }
        },
        output: {
            filename: 'js/program-reqs.js',
            chunkFilename: 'js/chunk-program-reqs.js',
        }
    },
    css: {
        extract: {
            filename: 'css/program-reqs.css',
            chunkFilename: 'css/chunk-program-reqs.css',
        }
    }
}