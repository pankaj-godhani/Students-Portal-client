const path = require('path');

module.exports = {
    entry: './resources/js/app.js',  // Update this to your entry point
    output: {
        filename: 'bundle.js',  // Update this to your desired output file
        path: path.resolve(__dirname, 'dist'),  // Update this to your desired output directory
    },
    resolve: {
        extensions: ['.js', '.jsx', '.vue', '.json']
    },

    plugins: [
        // Add plugins here as needed
    ]
};
