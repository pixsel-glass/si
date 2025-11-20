(function () {
	'use strict';

	const storage = getAvailableStorage();

	function getAvailableStorage() {
		const storageTypes = ['localStorage', 'sessionStorage'];
		for (const storageType of storageTypes) {
			if (storageAvailable(storageType)) {
				return window[storageType];
			}
		}
		console.error('Both localStorage and sessionStorage are not available.');
		return null;
	}

	function storageAvailable(storageType) {
		try {
			const storage = window[storageType];
			const testKey = '__test__';
			storage.setItem(testKey, testKey);
			storage.removeItem(testKey);
			return true;
		} catch (e) {
			return false;
		}
	}

	function setData(key, value, expiryDays = null) {
		const data = { value };
		if (expiryDays) {
			const expirationDate = new Date();
			expirationDate.setTime(expirationDate.getTime() + (expiryDays * 24 * 60 * 60 * 1000));
			data.expiry = expirationDate.getTime();
		}
		storage.setItem(key, JSON.stringify(data));
	}

	function getData(key) {
		const data = JSON.parse(storage.getItem(key));
		if (data && (!data.expiry || new Date().getTime() <= data.expiry)) {
			return data.value;
		}
		removeData(key);
		return null;
	}

	function removeData(key) {
		storage.removeItem(key);
	}

	const initTelInput = (telInput, options) => {
		const IntlTelInputSelectedCountryCookie = `IntlTelInputSelectedCountry_${telInput.dataset.phoneInputId}`;

		window.intlTelInputGlobals.autoCountry = getData(IntlTelInputSelectedCountryCookie) || window.intlTelInputGlobals.autoCountry;

		// fix autofill bugs on page refresh in Firefox
		const form = telInput.closest('form');
		if (form) {
			form.setAttribute('autocomplete', 'off');
		}

		const handleGeoIpLookup = (success, failure) => {
			let country = getData(IntlTelInputSelectedCountryCookie);
			if (!country) {
			fetch('https://ipapi.co/json')
				.then((res) => res.json())
				.then((data) => {
				country = data.country_code?.toUpperCase();
				success(country);
				setData(IntlTelInputSelectedCountryCookie, country, 7);
				})
				.catch(() => success('ua'));
			} else {
			success(country);
			}
		};

		if (options.geoIpLookup == null) {
			delete options.geoIpLookup; 
		} else if (options.geoIpLookup === 'ipapi') {
			options.geoIpLookup = handleGeoIpLookup;
		} else if (typeof window[options.geoIpLookup] === 'function') {
			options.geoIpLookup = window[options.geoIpLookup];
		} else {
			if (typeof options.geoIpLookup !== 'function') {
				throw new TypeError(
					`SAP-Tel-Input: Undefined function '${options.geoIpLookup}' specified in tel-input.options.geoIpLookup.`
				);
			}
			delete options.geoIpLookup;
		}

		if (options.customPlaceholder == null) {
			delete options.customPlaceholder; 
		} else if (typeof window[options.customPlaceholder] === 'function') {
			options.customPlaceholder = window[options.customPlaceholder];
		} else {
			if (typeof options.customPlaceholder !== 'function') {
				throw new TypeError(
					`SAP-Tel-Input: Undefined function '${options.customPlaceholder}' specified in tel-input.options.customPlaceholder.`
				);
			}
			delete options.customPlaceholder;
		}

		if (options.utilsScript) {
			options.utilsScript = options.utilsScript.charAt(0) === '/' ? options.utilsScript : '/' + options.utilsScript;
		}

		const itiPhone = window.intlTelInput(telInput, options);

		const countryChangeEventFunc = function () {
			const countryData = itiPhone.getSelectedCountryData();
			if (countryData.iso2) {
				setData(IntlTelInputSelectedCountryCookie, countryData.iso2?.toUpperCase(), 7);
				if (this.dataset.phoneCountryInput && countryData.iso2) {
					const phoneCountryInput = document.querySelector(this.dataset.phoneCountryInput);
					if (phoneCountryInput) {
						let oldValue = phoneCountryInput.value?.trim();
						phoneCountryInput.value = countryData.iso2?.toUpperCase();
						if (phoneCountryInput.value !== oldValue || phoneCountryInput.value != '') {
							phoneCountryInput.dispatchEvent(new KeyboardEvent('change'));
						}
					}
				}
				if (this.dataset.phoneDialCodeInput && countryData.dialCode) {
					const phoneDialCodeInput = document.querySelector(this.dataset.phoneDialCodeInput);
					if (phoneDialCodeInput) {
						let oldValue = phoneDialCodeInput.value;
						phoneDialCodeInput.value = countryData.dialCode;
						if (phoneDialCodeInput.value !== oldValue || phoneDialCodeInput.value != '') {
							phoneDialCodeInput.dispatchEvent(new KeyboardEvent('change'));
						}
					}
				}
				telInput.dispatchEvent(new KeyboardEvent('change'));
			}
		}

		const telInputChangeEventFunc = function () {
			if (this.dataset.phoneInput) {
				const phoneInput = document.querySelector(this.dataset.phoneInput);
				if (phoneInput) {
					let oldValue = phoneInput.value?.trim();
					if (oldValue != '' && oldValue.charAt(0) != '+'  && oldValue.charAt(0) != '0' && itiPhone.isValidNumber() === null) {
						oldValue = `+${oldValue}`;
						phoneInput.value = oldValue;
					}
					if (itiPhone.getNumber()?.trim() != '') {
						if (itiPhone.isValidNumber()) {
							phoneInput.value = itiPhone.getNumber();
						} else {
							phoneInput.value = '';
						}
					} else {
						if (oldValue != '' && itiPhone.isValidNumber() === null) {
							itiPhone.setNumber(oldValue);
							phoneInput.value = itiPhone.getNumber();
						}
					}
					if (phoneInput.value !== oldValue && phoneInput.value != '' && (itiPhone.isValidNumber() === true || itiPhone.isValidNumber() === null)) {
						phoneInput.dispatchEvent(new KeyboardEvent('change'));
						phoneInput.dispatchEvent(new KeyboardEvent('input'));
						phoneInput.dispatchEvent(new CustomEvent('telchange', {
							detail: {
								valid: true,
								validNumber: phoneInput.value,
								number: itiPhone.getNumber(),
								country: itiPhone.getSelectedCountryData().iso2?.toUpperCase(),
								countryName: itiPhone.getSelectedCountryData().name,
								dialCode: itiPhone.getSelectedCountryData().dialCode
							}
						}));
					} else {
						if (itiPhone.isValidNumber() === false) {
							phoneInput.dispatchEvent(new KeyboardEvent('change'));
							phoneInput.dispatchEvent(new KeyboardEvent('input'));
							phoneInput.dispatchEvent(new CustomEvent('telchange', {
								detail: {
									valid: false,
									validNumber: phoneInput.value,
									number: itiPhone.getNumber(),
									country: itiPhone.getSelectedCountryData().iso2?.toUpperCase(),
									countryName: itiPhone.getSelectedCountryData().name,
									dialCode: itiPhone.getSelectedCountryData().dialCode
								}
							}));
						}
					}
				}
			}
		}

		telInput.removeEventListener('countrychange', countryChangeEventFunc);
		telInput.addEventListener('countrychange', countryChangeEventFunc);
		telInput.removeEventListener('change', telInputChangeEventFunc);
		telInput.addEventListener('change', telInputChangeEventFunc);
		telInput.addEventListener('keyup', telInputChangeEventFunc);

		if (telInput.dataset.phoneInput) {
			const phoneInput = document.querySelector(telInput.dataset.phoneInput);
			if (phoneInput) {
				let oldValue = phoneInput.value?.trim();
				if (oldValue != '' && oldValue.charAt(0) != '+' && oldValue.charAt(0) != '0') {
					oldValue = `+${oldValue}`;
				}
				const changeHandler = function () {
					let newValue = this.value?.trim();
					if (newValue != oldValue && newValue != '') {
						itiPhone.setNumber(newValue);
					}
				};
				phoneInput.removeEventListener('change', changeHandler);
				phoneInput.addEventListener('change', changeHandler);
			}
		}

		if (telInput.dataset.phoneCountryInput) {
			const phoneCountryInput = document.querySelector(telInput.dataset.phoneCountryInput);
			if (phoneCountryInput) {
				const changeHandler = function () {
					itiPhone.setCountry(this.value?.trim());
				};
				phoneCountryInput.removeEventListener('change', changeHandler);
				phoneCountryInput.addEventListener('change', changeHandler);
			}
		}

		telInput.dispatchEvent(new KeyboardEvent('countrychange'));
	}

	const processedElements = new Set();
	let resourcesLoaded = false;
	const observerConfig = { root: null, rootMargin: '0px', threshold: 0.5 };

	const getConfig = (configName) => window[configName] || null;

	const loadResource = (tag, attributes) => {
		return new Promise((resolve, reject) => {
			const resource = Object.assign(document.createElement(tag), attributes);
			resource.onload = resolve;
			resource.onerror = reject;
			document.head.appendChild(resource);
		});
	};

	const loadTelInputResources = () => {
		if (!resourcesLoaded) {
			const stylePromise = loadResource('link', {
				rel: 'stylesheet',
				type: 'text/css',
				href: 'catalog/view/javascript/tel-input/css/intlTelInput.css',
			});

			const scriptPromise = loadResource('script', {
				src: 'catalog/view/javascript/tel-input/js/intlTelInput.min.js',
			});

			return Promise.all([stylePromise, scriptPromise])
				.then(() => {
					resourcesLoaded = true;
				})
				.catch((error) => {
					console.error('Error loading resources:', error);
				});
		}
		return Promise.resolve();
	};

	const getTelInputConfig = (telInput) => {
		const configType = telInput.getAttribute('data-config-type');
		return getConfig(configType === 'sms' ? 'sapTelSMSInputConfig' : 'sapTelInputConfig');
	};

	const handleIntersection = (entries, observer) => {
		entries.forEach((entry) => {
			const telInput = entry.target;
			const resourcesLoaded = telInput.dataset.resourcesLoaded === 'true';
			if (entry.isIntersecting && !processedElements.has(telInput) && (!resourcesLoaded || !telInput.dataset.resourcesLoaded)) {
				telInput.dataset.resourcesLoaded = true;
				const telInputConfig = getTelInputConfig(telInput);
				loadTelInputResources()
					.then(() => {
						initTelInput(telInput, telInputConfig);
						processedElements.add(telInput);
						observer.unobserve(telInput);
					})
					.catch(error => {
						console.error('Error loading telInputResources:', error);
					});
			}
		});
	};

	const observer = new IntersectionObserver(handleIntersection, observerConfig);

	const initVisibleTelInputs = () => {
		const telInputs = Array.from(document.querySelectorAll('.iti--sap-tel-input'))
			.filter((telInput) => !processedElements.has(telInput));

		telInputs.forEach((telInput) => observer.observe(telInput));

		if (resourcesLoaded) {
			telInputs.forEach((telInput) => {
				if (!processedElements.has(telInput)) {
					const telInputConfig = getTelInputConfig(telInput);
					initTelInput(telInput, telInputConfig);
					processedElements.add(telInput);
					observer.unobserve(telInput);
				}
			});
		}
	};

	document.addEventListener('DOMContentLoaded', () => {
		initVisibleTelInputs();

		//$(document).ajaxComplete(initVisibleTelInputs);
		document.addEventListener('ajaxComplete', initVisibleTelInputs);
	});
	
	$(document).on('shown.bs.modal', '#login, #login2', () => {
		if (!resourcesLoaded) {
			resourcesLoaded = false;
			loadTelInputResources().then(initVisibleTelInputs);
		}
	});
})();