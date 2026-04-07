/**
 * DigitalPersona U.are.U (WebSdk + @digitalpersona/* UMD bundles must load before this file).
 * Requires DigitalPersona Lite Client / Workstation on the PC so the local agent can talk to the reader.
 */
(function () {
    var busy = false;

    function liteClientHint() {
        return ' Cannot talk to the fingerprint reader. Install/start HID DigitalPersona Lite Client (or Workstation), plug in the U.are.U 4500, then try again.\n\nDownload: https://crossmatch.hid.gl/lite-client';
    }

    function ensureSdk() {
        if (typeof window.WebSdk === 'undefined') {
            return 'DigitalPersona WebSdk script is not loaded.' + liteClientHint();
        }
        if (!window.dp || !window.dp.devices || !window.dp.devices.FingerprintReader) {
            return 'DigitalPersona devices library is not loaded (check script order: WebSdk → core → devices).' + liteClientHint();
        }
        return null;
    }

    function samplesToTemplateString(samples) {
        if (samples == null) return '';
        if (typeof samples === 'string') return samples;
        if (Array.isArray(samples)) {
            if (samples.length === 0) return '';
            var first = samples[0];
            if (typeof first === 'string') return first;
            if (first && typeof first === 'object' && first.Data) return String(first.Data);
            return JSON.stringify(samples);
        }
        if (typeof samples === 'object' && samples.Data) return String(samples.Data);
        try {
            return JSON.stringify(samples);
        } catch (e) {
            return String(samples);
        }
    }

    /**
     * @param {string} fieldId - textarea id to fill
     * @returns {Promise<void>}
     */
    window.rehabDpCaptureToField = function (fieldId) {
        var msg = ensureSdk();
        if (msg) {
            alert(msg);
            return Promise.reject(new Error(msg));
        }
        var el = document.getElementById(fieldId);
        if (!el) {
            return Promise.reject(new Error('Missing field #' + fieldId));
        }
        if (busy) {
            alert('A fingerprint capture is already running. Wait for it to finish.');
            return Promise.reject(new Error('busy'));
        }
        busy = true;

        var reader = new window.dp.devices.FingerprintReader();
        var SampleFormat = window.dp.devices.SampleFormat;
        var fmt = SampleFormat.Intermediate;

        function cleanup() {
            try {
                reader.off();
            } catch (e) { /* ignore */ }
            busy = false;
        }

        alert('Place the finger on the U.are.U reader…');

        return new Promise(function (resolve, reject) {
            var settled = false;
            function finishOk(text) {
                if (settled) return;
                settled = true;
                resolve(text || '');
            }
            function finishErr(err) {
                if (settled) return;
                settled = true;
                reject(err);
            }
            function onSamples(ev) {
                var text = samplesToTemplateString(ev.samples);
                if (text) el.value = text;
                reader.stopAcquisition()
                    .then(function () { cleanup(); finishOk(text); })
                    .catch(function () { cleanup(); finishOk(text); });
            }
            function onError(ev) {
                var code = (ev && ev.error != null) ? ev.error : ((ev && ev.uError != null) ? ev.uError : 'Unknown reader error');
                alert('Fingerprint reader error: ' + code + liteClientHint());
                reader.stopAcquisition()
                    .then(function () { cleanup(); finishErr(new Error(String(code))); }, function () { cleanup(); finishErr(new Error(String(code))); });
            }
            function onCommFailed() {
                alert('Lost connection to DigitalPersona agent.' + liteClientHint());
                cleanup();
                finishErr(new Error('CommunicationFailed'));
            }
            reader.on('SamplesAcquired', onSamples);
            reader.on('ErrorOccurred', onError);
            reader.on('CommunicationFailed', onCommFailed);

            reader.startAcquisition(fmt).catch(function (e) {
                var m = (e && e.message) ? e.message : String(e);
                alert('Could not start fingerprint capture: ' + m + liteClientHint());
                cleanup();
                finishErr(e);
            });
        });
    };
})();
