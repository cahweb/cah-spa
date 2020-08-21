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
            contentBase: '/wp-content/themes/cah-spa/audition-forms/theatre-audition-form/dist/',
            allowedHosts: ['localhost/wordpress'],
            headers: {
                'Access-Control-Allow-Origin': '*',
            }
        },
        output: {
            filename: 'js/theatre-audition-form.js',
            chunkFilename: 'js/chunk-theatre-audition-form.js',
        }
    },
    css: {
        extract: {
            filename: 'css/theatre-audition-form.css',
            chunkFilename: 'css/chunk-theatre-audition-form.css',
        }
    }
}