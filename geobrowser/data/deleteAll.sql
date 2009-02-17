DELETE SiteTree, Page FROM SiteTree, Page  WHERE ClassName IN ('BrowseCitiesPage', 'BrowseContinentsPage', 'BrowseCountriesPage', 'BrowseRegionsPage', 'BrowseWorldPage') AND Page.ID = SiteTree.ID;
DELETE SiteTree_Live, Page_Live FROM SiteTree_Live, Page_Live WHERE ClassName IN ('BrowseCitiesPage', 'BrowseContinentsPage', 'BrowseCountriesPage', 'BrowseRegionsPage', 'BrowseWorldPage') AND Page_Live.ID = SiteTree_Live.ID;
DELETE SiteTree_versions, Page_versions FROM SiteTree_versions, Page_versions WHERE ClassName IN ('BrowseCitiesPage', 'BrowseContinentsPage', 'BrowseCountriesPage', 'BrowseRegionsPage', 'BrowseWorldPage') AND Page_versions.ID = SiteTree_versions.ID;

DELETE Page FROM Page LEFT JOIN SiteTree ON SiteTree.ID = Page.ID WHERE SiteTree.ID IS NULL;
DELETE Page_Live FROM Page_Live LEFT JOIN SiteTree_Live ON SiteTree_Live.ID = Page_Live.ID WHERE SiteTree_Live.ID IS NULL;
DELETE Page_versions FROM Page_versions LEFT JOIN SiteTree_versions ON SiteTree_versions.ID = Page_versions.ID WHERE SiteTree_versions.ID IS NULL ;

delete from browseabstractpage;
delete from browsecitiespage;
delete from browsecontinentspage;
delete from browsecountriespage;
delete from browseregionspage;

delete from browseabstractpage_Live;
delete from browsecitiespage_Live;
delete from browsecontinentspage_Live;
delete from browsecountriespage_Live;
delete from browseregionspage_Live;

delete from browseabstractpage_versions;
delete from browsecitiespage_versions;
delete from browsecontinentspage_versions;
delete from browsecountriespage_versions;
delete from browseregionspage_versions;