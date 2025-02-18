document.addEventListener("alpine:init", function () {
  function generateProxyComponent(context) {
    const obj = {
      get: function (target, method) {
        return (...args) => context.$call(method, ...args);
      },
    };

    return new Proxy({}, obj);
  }
  async function serverMethodCall({ component, method, args, csrf }) {
    return await fetch("/_navalha/update", {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf,
        "X-Navalha": 1,
      },
      body: JSON.stringify({ component, method, args }),
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
      $errors(name) {
        return name === undefined ? Object.entries(internal.errors).flatMap(x => x[1]) : (internal.errors[name] ?? [])[0];
      },
      $busy(name) {
        if (undefined === name) {
          return internal.loading !== null;
        }
      },
      $json: (value) => JSON.stringify(value, null, "\t"),
      get $navalha() {
        return generateProxyComponent(this);
      },
      async $call(method, ...args) {

        internal.loading = method;

        const response = await serverMethodCall({
          component,
          method,
          args,
          // payload: laravelData,
          csrf
        });

        try {

          internal.errors = [];

          const content = await response.json();

          if (response.status === 422) {
            internal.errors = content.errors;
          } else if (response.status >= 400) {
            this.$dispatch("navalha-error", content);
            return;
          }

          Object.assign(this, content.data);

        } finally {
          internal.loading = null;
        }
      },
    };
  });
});
