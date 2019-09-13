const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCssAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

exports.extractCSS = ({ filename, chunkFilename }) => ({
  plugins: [
    new MiniCssExtractPlugin({
      filename,
      chunkFilename
    })
  ]
});

exports.optimizeCSS = () => ({
  plugins: [
    new OptimizeCssAssetsPlugin({
      cssProcessor: require("cssnano"),
      cssProcessorOptions: { discardComments: { removeAll: true } },
      canPrint: true
    })
  ]
});

exports.cleanBuild = () => ({
  plugins: [new CleanWebpackPlugin()]
});
