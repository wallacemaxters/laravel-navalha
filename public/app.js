document.addEventListener('alpine:init', function () {

    Alpine.data('__navalha_component__', function (component, data) {

        return {
            ...data,
            $busy: {},
            $call: async function (method, ...args) {

                this.$busy[method] = true;

                const response = await fetch('/_navalha/update', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
                    },
                    body: JSON.stringify({ component, method, args })

                });

                try {
                    const content = await response.json();
                    Object.assign(this, content.data);
                } finally {
                    this.$busy[method] = false;
                }

            }
        };
    });
})
