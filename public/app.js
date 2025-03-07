document.addEventListener("alpine:init", function () {

    function makeNavalhaProxy(obj) {

        return new Proxy(obj, {

            get: function ({ internal, context, component, csrf, laravelData }, prop) {

                if (prop === '$errors') {
                    return (name) => undefined === name ? Object.entries(internal.errors).flatMap(x => x[1]) : (internal.errors[name] ?? [])[0];
                } else if (prop === '$busy') {
                    return (name) => (undefined === name) ? internal.loading !== null : internal.loading === name;
                } else if (prop === '$json') {
                    return (value) => JSON.stringify(value, null, "\t");
                } else if (prop === '$upload') {
                    return (method, files, ...payload) => navalhaMethodCall({
                        component,
                        context,
                        csrf,
                        internal,
                        method,
                        files,
                        payload
                    })
                }

                return (...payload) => navalhaMethodCall({
                    component,
                    context,
                    csrf,
                    internal,
                    method: prop,
                    // laravelData,
                    payload
                });
            }
        });
    }

    async function navalhaMethodCall({ context, internal, csrf, component, method, files, payload, laravelData }) {

        internal.loading = method;

        const response = await serverMethodCall({
            component,
            method,
            payload,
            files,
            csrf,
            laravelData
        });

        try {

            internal.errors = [];

            const content = await response.json();

            if (response.status === 422) {
                internal.errors = content.errors;
            } else if (response.status >= 400) {
                context.$dispatch("navalha-error", content);
                return;
            }

            if (content.component) {
                Object.assign(context, content.data);
            } else {
                return content;
            }

        } finally {
            internal.loading = null;
        }
    }

    async function serverMethodCall({ component, method, payload, files, csrf, laravelData }) {

        const form = new FormData();
        form.append('component', component);
        form.append('method', method);

        if (files instanceof File || files instanceof Blob) {
            files = [files];
        }

        files && Array.from(files).forEach((file, index) => {
            form.append(`files[${index}]`, file, file.name);
        });

        if (payload instanceof FormData) {
            Array.from(payload.entries()).forEach(([name, value]) => {
                form.append(`args[${name}]`, value);
            });

        } else {
            form.append('payload', JSON.stringify(payload));
        }

        laravelData && form.append('laravelData', JSON.stringify(laravelData));

        return await fetch("/_navalha/update", {
            method: "POST",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrf,
                "X-Navalha": 1,
            },
            body: form,
        });
    }

    Alpine.data("__navalha_component__", function ({ component, data, csrf }) {

        const internal = Alpine.reactive({
            loading: null,
            errors: {}
        });

        const laravelData = Alpine.reactive(data);

        return {
            ...laravelData,
            get $navalha() {
                return makeNavalhaProxy({ internal, csrf, component, context: this, laravelData })
            },
            get $n() {
                return this.$navalha;
            }
        };
    });
});
