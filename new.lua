function ev.onServerMessage(color, text)
  if text:find("Администратор (.+) ответил вам") then
    str0 = text:match("ответил вам: (.+)")
    for i=1, #array do
      if array[i] == str0 then
        sampAddChatMessage("Ответ есть в массиве", -1)
      end
    end
  end
end