(function(window)
{
	"use strict";

	const STORAGE_PRICE_ID_KEY = 'SECTIONS_EXTRA_SAVED_PRICE_ID';

	class Store 
	{
		constructor(data)
		{
			this.columns = data.columns;
			this.rows = data.rows;
			this.prices = data.prices;

			this.changes = [];
			this.priceId = null;

			this.initPrices();
			this.initRows(this.rows);
		}

		initPrices()
		{
			if (window.localStorage)
			{
				this.priceId = parseInt(localStorage.getItem(STORAGE_PRICE_ID_KEY));
			}

			if (!this.priceId)
			{
				for (var i in this.prices)
				{
					if (this.prices.hasOwnProperty(i))
					{
						this.priceId = this.selectPrice(this.prices[i]['ID']);
						break;
					}
				}
			}
		}

		initRows(rows, namePrefix = '')
		{   
			rows.forEach((row, index) => {
				row._EXTRA = B2BPortal.Utils.cloneDeep(row.EXTRA);
				row._NAME = row.NAME;

				row.NAME = namePrefix + (index + 1) + '. ' + row._NAME;

				if ((row.CHILDREN || []).length)
				{
					this.initRows(row.CHILDREN, namePrefix + (index + 1) + '. ');
				}
			});
		}

		
		addChange(data)
		{
			for (var i in this.changes)
			{
				if (this.changes.hasOwnProperty(i))
				{
					if (
						this.changes[i].priceId == data.priceId &&
						this.changes[i].row.ID == data.row.ID
					)
					{
						this.changes.splice(i, 1);
						break;
					}
				}
			}

			if (data.oldValue != data.newValue)
			{
				if (!data.price)
				{
					data.price = this.prices[data.priceId];
				}

				this.changes.push(data);
			}
		}

		clearChanges()
		{
			this.changes = [];
		}

		resetExtraValues(rows, priceId)
		{
			if (priceId)
			{
				rows.forEach(row => {
					row.EXTRA[priceId] = B2BPortal.Utils.cloneDeep(row._EXTRA[priceId]);
				});
			}
			else
			{
				rows.forEach(row => {
					row.EXTRA = B2BPortal.Utils.cloneDeep(row._EXTRA);
					
					if ((row.CHILDREN || []).length)
					{
						this.resetExtraValues(row.CHILDREN);
					}
				});
			}
		}

		setExtraValues(row, values)
		{
			const groupedValues = B2BPortal.Utils.groupBy(values, 'PRICE_ID');

			for (var priceId in groupedValues)
			{
				if (row._EXTRA[priceId])
				{
					row._EXTRA[priceId].VALUE = row.EXTRA[priceId].VALUE = groupedValues[priceId][0].VALUE;
					row._EXTRA[priceId].ID = row.EXTRA[priceId].ID = groupedValues[priceId][0].ID;
				}
			}
		}

		setExtrasValues(rows, values)
		{
			rows
				.filter((row) => values[row.ID])
				.forEach((row) => this.setExtraValues(row, values[row.ID]));

			rows
				.filter((row) => (row.CHILDREN || []).length)
				.forEach((row) => this.setExtrasValues(row.CHILDREN, values))
		}
		
		completeChanges(completed= [])
		{
			this.changes = this.changes.filter((change) => {
				const priceId = change.priceId;
				const extra = change.row.EXTRA[priceId];

				return !completed.includes(extra.ID);
			});
		}

		resetChanges()
		{
			this.clearChanges();
			this.resetExtraValues(this.rows);
		}

		selectPrice(id)
		{
			this.priceId = parseInt(id);

			if (window.localStorage)
			{
				localStorage.setItem(STORAGE_PRICE_ID_KEY, this.priceId);
			}
		}
	}


	window.SectionsExtraStore = Store;

})(window, B2BPortal);