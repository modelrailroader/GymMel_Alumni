const path = require('path');

module.exports = {
  entry: './GymMel_Alumni/assets/js/index.js',
  output: {
    filename: 'main.js',
    path: path.resolve(__dirname, 'GymMel_Alumni/assets/dist')
  },
  mode: 'development',
  watchOptions: {
    aggregateTimeout: 200,
    poll: 1000
  },
  devtool: false
};


