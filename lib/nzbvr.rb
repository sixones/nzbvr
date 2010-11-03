module NZBvr
  ROOT = File.expand_path(File.join(File.dirname(__FILE__), '..'))
  LIB_DIR = File.expand_path(File.join(ROOT, 'lib'))
  NZBVR_DIR = File.expand_path(File.join(LIB_DIR, 'nzbvr'))
  MODELS_DIR = File.expand_path(File.join(NZBVR_DIR, 'models'))
  
  autoload :Integrator, "#{NZBVR_DIR}/integrator.rb"
  
  module Models
    # loaded through integrator
  end
end