module.exports = {
    productionSourceMap: false,
    publicPath: process.env.NODE_ENV === 'production'
        ? 'wordpress/wp-content/themes/cah-spa/audition-forms/music-audition-form/dist'
        : 'http://localhost:8080',
    devServer: {
        proxy: 'http://localhost/wordpress/wp-content/themes/cah-spa/',
    },
    outputDir: './dist',
    configureWebpack: {
        devServer: {
            contentBase: '/wp-content/themes/cah-spa/audition-forms/music-audition-form/dist/',
            allowedHosts: ['localhost/wordpress'],
            headers: {
                'Access-Control-Allow-Origin': '*',
            }
        },
        output: {
            filename: 'js/music-audition-form.js',
            chunkFilename: 'js/chunk-music-audition-form.js',
        }
    },
    css: {
        extract: {
            filename: 'css/music-audition-form.css',
            chunkFilename: 'css/chunk-music-audition-form.css',
        }
    }
}