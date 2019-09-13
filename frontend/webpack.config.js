const merge = require("webpack-merge");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const plugins = require("./webpack.plugins");

const commonConfig = merge([
  {
    entry: {
      admin: "./src/admin.js",
      block: "./src/block.js",
      public: "./src/public.js"
    },
    output: {
      filename: "[name].js"
    },
    module: {
      rules: [
        {
          test: /\.(js|jsx)$/,
          use: "eslint-loader",
          enforce: "pre"
        },
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader"
          }
        },
        {
          test: /\.(css|scss)$/,
          use: [
            MiniCssExtractPlugin.loader,
            "css-loader",
            "postcss-loader",
            "sass-loader"
          ]
        },
        {
          test: /\.(png|jpe?g|svg|gif)$/i,
          loader: "file-loader",
          options: {
            name: "[name].[ext]",
            outputPath: "assets"
          }
        }
      ]
    }
  },
  plugins.extractCSS({
    filename: "[name].css",
    chunkFilename: "[id].css"
  })
]);

const productionConfig = merge([plugins.cleanBuild(), plugins.optimizeCSS()]);
const developmentConfig = merge([{ devtool: "cheap-module-source-map" }]);

module.exports = (env, argv) => {
  if (argv.mode === "production") {
    return merge(commonConfig, productionConfig);
  }

  return merge(commonConfig, developmentConfig);
};
