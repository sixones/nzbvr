require 'optparse'

module NZBvr
  class Runner
    COMMANDS = %w[ start ]
    
    attr_accessor :options, :arguments, :integrator
    private :options, :arguments, :integrator

    include Logger

    def initialize(argv)
      @arguments = argv
      @integrator = NZBvr::Integrator.new

      parse!
    end

    def option_parser
      @parser ||= OptionParser.new { |opts|
        opts.banner = "Usage: nzbvr [options]"

        opts.separator ""
        opts.separator "Web-server options:"

        opts.on("-a", "--address HOST", "Bind web server to host") { |host| @integrator.configuration[:server][:hostname] = host }
        opts.on("-p", "--port PORT", "Bind web server to port") { |port| @integrator.configuration[:server][:port] = port.to_i }
        
        opts.on("-d", "--debug", "Enable debug output in the logger") { @integrator.configuration[:debug] = true }
        opts.on("-s", "--silent", "Silence the logger output") { @integrator.configuration[:silent] = true }
        
        opts.on("-h", "--help", "Shows the usage information") { puts opts; exit }
        opts.on("-v", "--version", "Shows the nzbVR version") { puts "nzbVR #{NZBvr::version}"; exit }
      }
    end
    private :option_parser

    def parse!
      option_parser.parse! @arguments

      @command = @arguments.shift
      
      if @command == nil
        @command = "start"
      end
    end
    private :parse!

    def run!
      if COMMANDS.include?(@command)
        run_command
      else
        abort "Unknown command specified: #{@command}. Usage: nzbvr --help"
      end
    end

    def run_command
      Logger.silent = @integrator.configuration[:silent]
      Logger.debug = @integrator.configuration[:debug]

      info "nzbVR #{NZBvr::version}"
      debug "Debug output: ON"
      
      @integrator.initialize_configuration
      @integrator.initialize_database
    end
    private :run_command
  end
end