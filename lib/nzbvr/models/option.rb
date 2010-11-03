class NZBvr::Models::Option
  include DataMapper::Resource
  
  property :id, Serial
  property :name, String
  property :description, Text
  property :type, Enum[ :boolean, :string, :text, :float, :integer, :datetime, :object, :yaml, :json, :list ], :default => :string
  property :default_value, Object
  property :created_at, DateTime, :default => DateTime.now
  
end