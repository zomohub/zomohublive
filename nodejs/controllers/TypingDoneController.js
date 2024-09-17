const funcs = require('../functions/functions')
const compiledTemplates = require('../compiledTemplates/compiledTemplates')
const socketEvents = require('../events/events')
const { Sequelize, Op, DataTypes } = require("sequelize");
const striptags = require('striptags');
const moment = require("moment")

const TypingDoneController = async (ctx, data, io,socket) => {
  await socketEvents.typingDone(ctx, io, data, ctx.userHashUserId[data.user_id])
};

module.exports = { TypingDoneController };