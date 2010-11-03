class NZBvr::Models::Plugin
  include DataMapper::Resource
  
  property :id, Serial
  property :signature, String, :required => true
  property :name, String
  property :created_at, DateTime, :default => DateTime.now
  
  has n, :plugin_options
  has n, :option_values, :through => :plugin_options
end