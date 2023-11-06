(function () {

	if (!window.VBasket)
	{
		window.VBasket = {};
	}

	function runAction(actionName, data)
	{
		return new Promise(function(resolve, reject) {
			BX.ajax.runAction(
				'redsign:vbasket.api.userbasket.' + actionName, 
				{ 
					data: data
				}
			)
			.then(resolve, reject);
		});
	}
	
	window.VBasket.select = BX.throttle(function(code) {
		return runAction('select', {code: code})
			.then(function () {
				BX.reload();
			}, function (e) {
				console.error(e);
			});
	}, 1000);

}(window));