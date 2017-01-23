module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      dist: {
        options: {
          style: 'compressed'
        },
        files: {
          'web/css/main.css': 'src/css/main.scss'
        }
      }
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
        separator: ';'
      },
      build: {
        src: 'src/js/*js',
        dest: 'web/js/main.min.js'
      }
    },
    watch: {
      scripts: {
        files: 'src/js/*.js',
        tasks: ['uglify']
      },
      styles: {
        files: 'src/css/*.scss',
        tasks: ['sass:dist']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass')
  grunt.loadNpmTasks('grunt-contrib-uglify')
  grunt.loadNpmTasks('grunt-contrib-watch')

  grunt.registerTask('default', ['sass:dist', 'uglify']);

};