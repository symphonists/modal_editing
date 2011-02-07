# Modal Editing

## Usage

Load Symphony backend pages into a lightbox using the following:

	http://mysite.com/symphony/publish/articles/new/?lightbox=true&redirect=http://mysite.com

The URL accepts the following parameters:

* `lightbox` must always be `true`
* `redirect` is the URL to redirect to after creating an entry. If the lightbox uses an iframe, this redirect will break out and refresh the entire window
* `redirect-delete` if you want to have a different redirect URL for if the entry is deleted rather than saved