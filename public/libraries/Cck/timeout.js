/**
 * 便於 Async + Await 使用的 timeout 方法
 * @param {number} ms 毫秒
 * @returns {Promise}
 */
function timeout(ms) {
    return new Promise((resolve) => {
        setTimeout(resolve, ms);
    });
}
