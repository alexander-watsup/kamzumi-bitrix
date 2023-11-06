const path = require('path');

module.exports = {
  entry: './src/index.tsx',
  "mode": "production",
  output: {
    path: path.resolve(__dirname, '..', 'js'),
    filename: 'app.bundle.js'
  },
  resolve: {
    extensions: ['.js', '.jsx', '.ts', '.tsx'],
  },
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react', '@babel/preset-typescript']
          }
        },

      },
    ]
  }
};