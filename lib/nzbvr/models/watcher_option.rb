class NZBvr::Models::WatcherOption
  include DataMapper::Resource
  
  property :id, Serial
  property :watcher_id, Integer, :key => true, :required => true
  property :option_value_id, Integer, :key => true, :required => true
  
  belongs_to :watcher, :key => true
  belongs_to :option_value, :child_key => [ :option_value_id ], :key => true
end