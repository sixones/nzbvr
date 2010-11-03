module NZBvr
  module Logger
    class << self
      attr_writer :silent, :debug
      
      def silent?
        @silent ||= false
      end
      
      def debug?
        !@silent && (@debug ||= false)
      end
    end
    
    def info(message)
      Logger.log(:info, message) unless Logger.silent?
    end
    module_function :info
    public :info
    
    def debug(message)
      Logger.log(:debug, message) if Logger.debug?
    end
    module_function :debug
    public :debug

    def error(message)
      Logger.log(:error, message)
    end
    module_function :error
    public :error

    def fatal(message)
      Logger.log(:fatal, message)
    end
    module_function :fatal
    public :fatal
    
    def backtrace(e = $!)
      debug("#{e}\n\t"+e.backtrace.join("\n\t"))
    end
    module_function :backtrace
    public :backtrace
    
    def log(type, message)
      # [#{type.to_s.upcase}]
      if type == :error || type == :fatal
        puts ">> #{message}"
      else
        puts "<< #{message}"
      end
    end
    module_function :log
    private :log
  end
end