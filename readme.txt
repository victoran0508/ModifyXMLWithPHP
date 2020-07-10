One place you can be confused with is Locations. New file Locality > Sub Locality > Tower name.
And target format (old.xml) is Community (locality) > Tower name (property name)
So we skip sub Locality
Locality = Community
Tower name = property name
city = city
2nd confusing thing is link has property purpose BUY OR RENT = Ad_type SALE OR RENT
So property purpose (Buy) = ad_type Sale
property purpose (Rent) = ad_type Rent

The rest easy, just renames
Properties = Listings
Property = Listing

-<Property_Ref_No> in link -> <Unit_Reference_No> target





https://bcrm_org.s3.amazonaws.com/xml/275/1591607442_5_275_59411.xml -> bayut.xml