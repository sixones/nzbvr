require 'dm-core'
require 'dm-migrations'
require 'dm-types'

module NZBvr
  class Integrator
    include Logger

    require "#{NZBvr::MODELS_DIR}/option.rb"
    require "#{NZBvr::MODELS_DIR}/option_value.rb"
    require "#{NZBvr::MODELS_DIR}/plugin.rb"
    require "#{NZBvr::MODELS_DIR}/plugin_option.rb"
    require "#{NZBvr::MODELS_DIR}/watcher.rb"
    require "#{NZBvr::MODELS_DIR}/watcher_option.rb"
  
    def initialise
      # manage database connection
      # manage default configuration
    end
  
    ## Cached list of potential and valid installation paths 
    def installation_paths
      if @installation_paths == nil
        @installation_paths = [ ]
      
        possible_paths = [ File.expand_path("~/.nzbvr"), File.expand_path(NZBvr::ROOT) ]
      
        possible_paths.each { |path|
          @installation_paths << path unless File.exists?(path)
        }
      end
    
      @installation_paths
    end
  
    ## Searches all the installation paths for the specified file and returns the first
    ## full path to the file
    def installation_path_for(filename)
      self.installation_paths.each { |path|
        if Dir.exists?(path)
          file_path = File.join(path, filename)
      
          if File.exists?(file_path)
            return file_path
          end
        end
      }
  
      return nil
    end
  
    ## Global nzbVR configuration, used for core configuration options only
    def configuration
      @configuration ||= {
        :database => {
          :adapter => 'sqlite3',
          :hostname => 'localhost',
          :username => '',
          :password => '',
          :path => 'nzbvr.sqlite3'
        },
        :server => {
          :hostname => '0.0.0.0',
          :port => '8081'
        }
      }
    end
  
    ## Initialises the database using the :database hash in the configuration
    def initialize_database
      debug "Initializing database connection using configuration"
    
      DataMapper.setup(:default, self.configuration[:database])
      DataMapper.auto_upgrade!
    end
  
    ## Initialises the configuration using a config.yml in one of the installation directories
    ## or the default values
    def initialize_configuration
      config_path = installation_path_for("config.yml")
    
      if config_path == nil
        error "Unable to find configuration file in any installation paths"
      else
        info "Loading configuration from #{config_path}"
      end
    end
  end
end