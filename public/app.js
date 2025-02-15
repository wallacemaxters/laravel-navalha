document.addEventListener("alpine:init", function () {
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
      $busy: {},
      $call: async function (method, ...args) {
        this.$busy[method] = true;

        const response = await serverMethodCall({
          component,
          method,
          args,
          csrf,
        });

        try {
          const content = await response.json();
          Object.assign(this, content.data);
        } finally {
          this.$busy[method] = false;
        }
      },
    };
  });
});
