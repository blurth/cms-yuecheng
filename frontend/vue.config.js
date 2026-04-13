const path = require('path')

// gzip压缩插件
const CompressionPlugin = require('compression-webpack-plugin')
function resolve(dir) {
  return path.join(__dirname, dir)
}

function resolve(dir) {
  return path.join(__dirname, dir)
}

module.exports = {
  lintOnSave: false,
  productionSourceMap: false,
  // assetsDir: 'static',
  chainWebpack: config => {
    config.resolve.alias.set('@', resolve('src')).set('lin', resolve('src/lin')).set('assets', resolve('src/assets'))
    config.module.rule('ignore').test(/\.md$/).use('ignore-loader').loader('ignore-loader').end()
  },
  configureWebpack: {
    devtool: 'source-map',
    resolve: {
      extensions: ['.js', '.json', '.vue', '.scss', '.html'],
    },
  },
  css: {
    loaderOptions: {
      sass: {
        prependData: `@import "@/assets/style/shared.scss";`,
      },
    },
  },
  devServer: {},
  // node_modules依赖项es6语法未转换问题
  transpileDependencies: ['vuex-persist'],
  configureWebpack: config => {
    // 开启gzip压缩
    config.plugins.push(
      new CompressionPlugin({
        algorithm: 'gzip',
        test: new RegExp('\\.(' + ['js', 'css', 'png', 'jpg', 'jpeg'].join('|') + ')$'),
        threshold: 10240,
        minRatio: 0.8,
      }),
    )
  },
}
