class NZBvr::Models::PluginOption
  include DataMapper::Resource
  
  property :id, Serial
  property :plugin_id, Integer, :key => true, :required => true
  property :option_value_id, Integer, :key => true, :required => true
  
  belongs_to :plugin, :key => true
  belongs_to :option_value, :child_key => [ :option_value_id ], :key => true
end