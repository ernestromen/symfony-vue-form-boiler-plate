const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/symfony-vue-form-boiler-plate/public/build')
    .addEntry('app', './assets/js/app.js')  // Replace with your entry file
    .splitEntryChunks()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSingleRuntimeChunk()
    .configureBabel((config) => {
        // Customize Babel configuration if needed
    })
    .enableVueLoader();

module.exports = Encore.getWebpackConfig();
