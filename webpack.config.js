let Encore = require('@symfony/webpack-encore');

Encore
  .autoProvidejQuery()
  .addEntry('global.scripts', './app/Resources/js/scripts.js')
  .addEntry('resources.scripts', './app/Resources/js/resources.js')
  .addStyleEntry('global.style', './app/Resources/scss/global.scss')
  .addStyleEntry('home.style', './app/Resources/scss/components/theme/pages/_theme.home.scss')
  .addStyleEntry('resources.style', './app/Resources/scss/components/theme/pages/_theme.resources.scss')
  .addStyleEntry('news.style', './app/Resources/scss/components/theme/pages/_theme.latest-news.scss')
  .addStyleEntry('style', './web/src/scss/style.scss') // admin
  .enableSassLoader()
  .enablePostCssLoader((options) => {
    options.config = {
      path: 'postcss.config.js'
    }
  })
;

if (Encore.isProduction()) {
  Encore
    .setOutputPath('web/build/')
    .setPublicPath('/build')
    .enableVersioning()
  ;
} else {
  Encore
    .setOutputPath('web/build/')
    .setPublicPath('http://localhost:8080/')
    .setManifestKeyPrefix('build/')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps()
  ;
}

// export the final configuration
module.exports = Encore.getWebpackConfig();
