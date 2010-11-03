class NZBvr::Models::Watcher
  include DataMapper::Resource
  
  property :id, Serial
  property :name, String, :required => true
  property :created_at, DateTime, :default => DateTime.now
  property :deleted_at, ParanoidDateTime
  
  has n, :watcher_options
  has n, :option_values, :through => :watcher_options
end