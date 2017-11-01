let Encore = require('@symfony/webpack-encore');

Encore
  .autoProvidejQuery()
  .addStyleEntry('global', './app/Resources/scss/global.scss')
  .addStyleEntry('home', './app/Resources/scss/components/theme/pages/_theme.home.scss')
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
