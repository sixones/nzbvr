class NZBvr::Models::OptionValue
  include DataMapper::Resource
  
  property :id, Serial
  property :option_id, Integer, :key => true, :required => true
  property :value, Object, :required => true, :lazy => false
  
  belongs_to :option, :key => true
end