switch (document.readyState) {
	case "loading":
		document.addEventListener("DOMContentLoaded", (_) => {
			renderNumbers();
		});
		window.addEventListener("load", (_) => {
			showAssetSize();
		});
		break;
	case "interactive":
		renderNumbers();
		window.addEventListener("load", (_) => {
			showAssetSize();
		});
		break;
	default:
		showAssetSize();
		renderNumbers();
}

async function showAssetSize() {
	const speed = 150000; // bytes per second
	const navigation = performance.getEntriesByType('navigation');
	const resources = performance.getEntriesByType('resource');
	let totalSize = navigation.reduce((size, item) => {
		size += item.decodedBodySize;
		return size;
	}, 0);
	totalSize += resources.reduce((size, item) => {
		size += item.decodedBodySize;
		return size;
	}, 0);
	document.getElementById('asset-size').textContent = totalSize.toLocaleString(navigator.language, { style: "unit", unit: "byte", unitDisplay: "long" });
	document.getElementById('download-time').textContent = (totalSize / speed).toLocaleString(navigator.language, { style: "unit", unit: "second" });
}

async function renderNumbers() {
	const numberElements = document.getElementsByClassName('number');
	for (const numberElement of numberElements) {
		numberElement.textContent = Number(numberElement.textContent).toLocaleString(
			navigator.language,
			{ notation: "compact", maximumSignificantDigits: 3 }
		);
	}
}
