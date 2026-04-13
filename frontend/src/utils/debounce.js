/*
 * 防抖函数:debounce 指的是某个函数在某段时间内，无论触发了多少次回调，都只执行一次。这样就保证了回调函数之间的调用间隔，至少是xxx毫秒。
 * @param {Function} fn:相关执行的回调函数
 * @param {Number} wait:延迟时间，也就是阈值，单位为毫秒。即连续点击，函数执行需要等待的时间。
 * @param {Boolean} immediate:表示第一次是否立即执行函数fn。true-立即执行；false-第一次将在设置的延迟时间后执行函数。默认false。
 */
export default function debounce(fn, wait, immediate = true) {
  // console.log('debounce...');
  // console.log('wait...', wait);
  let context
  let args
  let timer
  let timestamp
  let result
  const later = function () {
    const last = new Date().getTime() - timestamp

    if (last < wait && last >= 0) {
      timer = setTimeout(later, wait - last)
    } else {
      timer = null

      if (!immediate) {
        result = fn.apply(context, args)
        context = args = null

        return result
      }
    }
  }

  const debounced = function () {
    context = this
    timestamp = new Date().getTime()
    args = arguments

    const callNow = immediate && !timer

    if (!timer) {
      timer = setTimeout(later, wait)
    }

    if (callNow) {
      result = fn.apply(context, args)

      context = args = null

      return result
    }
  }

  debounced.cancel = function () {
    clearTimeout(timer)

    timer = null
    context = args = null
  }

  return debounced
}
