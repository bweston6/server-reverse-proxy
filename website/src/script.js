switch (document.readyState) {
	case "loading":
		document.addEventListener("DOMContentLoaded", (_) => {
		});
		window.addEventListener("load", (_) => {
			showAssetSize();
		});
		break;
	case "interactive":
		window.addEventListener("load", (_) => {
			showAssetSize();
		});
		break;
	default:
		showAssetSize();
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
