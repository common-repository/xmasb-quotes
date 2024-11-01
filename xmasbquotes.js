// Ask the user before submiting the form
function delete_confirmation_xmasb_quotes() {
	if (confirm('You are about to delete this quote.\n\nThis cannot be undone. Are you sure?')) return true;
	else return false;
}
