require 'dm-core'
require 'dm-migrations'
require 'dm-types'

class NZBvr::Integrator

  require "#{NZBvr::MODELS_DIR}/option.rb"
  require "#{NZBvr::MODELS_DIR}/option_value.rb"
  require "#{NZBvr::MODELS_DIR}/plugin.rb"
  require "#{NZBvr::MODELS_DIR}/plugin_option.rb"
  require "#{NZBvr::MODELS_DIR}/watcher.rb"
  require "#{NZBvr::MODELS_DIR}/watcher_option.rb"
  
  def initialise
    # manage database connection
    # manage loading plugins?
    # manage defaults
  end
  
  def initialise_database
    DataMapper.setup(:default, { :adapter => "sqlite3", :hostname => "localhost", :path => "nzbvr.sqlite3" })
    DataMapper.auto_upgrade!
  end
end