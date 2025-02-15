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
    return {
      ...data,
      $loading: null,
      $busy(name) {
        if (undefined === name) {
          return this.$loading !== null;
        }
        return this.$loading === name;
      },
      $json: (value) => JSON.stringify(value, null, "\t"),
      get $navalha() {
        return generateProxyComponent(this);
      },
      async $call(method, ...args) {
        this.$loading = method;

        const response = await serverMethodCall({
          component,
          method,
          args,
          csrf,
        });

        try {

          const content = await response.json();

          if (response.status >= 400) {
            this.$dispatch("navalha-error", content);
            return;
          }
          Object.assign(this, content.data);
        } finally {
          this.$loading = null;
        }
      },
    };
  });
});
