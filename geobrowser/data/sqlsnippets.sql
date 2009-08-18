Update `SiteTree`, `countries`, `regions`, `cities`, `browseabstractpage`, `SiteTree` as Parent, `SiteTree` as GrandParent
Set SiteTree.URLSegment = GrandParent.URLSegment, Parent.URLSegment
WHERE 
browseabstractpage.ID = SiteTree.ID AND
ClassName = 'BrowseCitiesPage' AND 
cities.CityID = browseabstractpage.HiddenDataID AND
cities.RegionID = regions.RegionID AND
cities.CountryID = countries.CountryID
LIMIT 10
