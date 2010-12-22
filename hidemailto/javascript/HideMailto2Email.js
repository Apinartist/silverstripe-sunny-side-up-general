function HideMailto2Email(dotreplacer, name, url, urlEncodedSubject) {
	name = name.replace(dotreplacer, ".");
	url = url.replace(dotreplacer, ".");
	var email = name+"@";
	if(urlEncodedSubject) {
		email += "?subject="+urlEncodedSubject;
	}
	return email
}
