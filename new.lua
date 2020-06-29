--[[function ev.onServerMessage(color, text)
  if text:find("Администратор (.+) ответил вам") then
    str0 = text:match("ответил вам: (.+)")
    for i=1, #array do
      if array[i] == str0 then
        sampAddChatMessage("Ответ есть в массиве", -1)
      end
    end
  end
end]] -- не работает

local imgui = require 'imgui'
local key = require 'vkeys'
local encoding = require 'encoding'
encoding.default = 'CP1251'
u8 = encoding.UTF8
local update = false
local fAlpha = 0.00
local text = "Шо?"
local renderMenu = imgui.ImBool(false)
local canRender = imgui.ImBool(false)
function main()
while not isSampAvailable() do wait(0) end wait(5)
    while true do wait(0)
    if (isKeyJustPressed(VK_F2)) then renderMenu.v = not renderMenu.v end
    UpdateAlpha(renderMenu.v)
    if (fAlpha ~= 0.00) then canRender.v = true else canRender.v = false end -- вверх можете пихнуть, мне чиста пихуй
    imgui.Process = canRender.v
    end
end

function UpdateAlpha(menustate)
    if (menustate) then -- open
        if (fAlpha ~= 1.00)then fAlpha = fAlpha + 0.05 end
    else -- close
        if (fAlpha ~= 0.00)then fAlpha = fAlpha - 0.05 end
    end
    if (fAlpha > 1.00) then fAlpha = 1.00 end
    if (fAlpha < 0.00) then fAlpha = 0.00 end -- anti dowen fix
    apply_custom_style()
end
function imgui.OnDrawFrame()
        ScreenX, ScreenY = getScreenResolution()
        imgui.SetNextWindowPos(imgui.ImVec2(ScreenX / 2 , ScreenY / 2 ), imgui.Cond.FirstUseEver, imgui.ImVec2(0.5, 0.5))
        imgui.SetNextWindowSize(imgui.ImVec2(350, 200), imgui.Cond.FirstUseEver)
        imgui.Begin(u8"Animat1on DIMANSTATION BY rraggerr for blAst.hk", renderMenu, imgui.WindowFlags.NoCollapse + imgui.WindowFlags.NoResize)
        imgui.Text(u8"Як красива украiна")
        if imgui.Button(u8(text)) then
        text = "Нишо!"
        end
        imgui.End()
end

inik = {
  cfg_am = {
    key = ":",
    key2 = "Администратор",
    autoreconnect = false,
    logs = true
  }
}

local key1 = config.cfg_am.key
local key2 = config.cfg_am.key2

function ev.onServerMessage(color, text)
  if text:find(key1) and text:find(key2) then
    lua_thread.create(function()
      math.randomseed(os.time())
      timewait = math.random(3000, 5500)
      wait(timewait)
      sampAddChatMessage("типа послан ответ", -1)
    end)
  end
end