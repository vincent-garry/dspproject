const Encore = require("@symfony/webpack-encore");

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore.setOutputPath("public/build/")
  .setPublicPath("/build")
  .addEntry("app", "./assets/app.js")
  .addStyleEntry("styles", "./style/style.scss")
  .enableStimulusBridge("./assets/controllers.json")
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = "usage";
    config.corejs = "3.23";
  })
  .enableSassLoader()
  .configureCssLoader((options) => {
    options.modules = true;
  })
  .configureDevServerOptions((options) => {
    options.devMiddleware = {
      writeToDisk: true,
    };
  });

module.exports = Encore.getWebpackConfig();
