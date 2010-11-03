require 'rake'

desc 'Start an IRB session with the nzbVR library on the include path'
task :shell do
  chdir File.dirname(__FILE__)
  
  exec 'irb -I lib/ -I lib/nzbvr -r rubygems'
end