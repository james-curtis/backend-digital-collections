export default function h5Copy(content) {
  
  let input = document.createElement("input")
  input.value = content
  input.readOnly = "readOnly"
  document.body.appendChild(input)
  input.select() // 选择对象
  input.setSelectionRange(0, content.length) //核心
  let result = document.execCommand("Copy") // 执行浏览器复制命令
  input.remove()
  return result
  
}